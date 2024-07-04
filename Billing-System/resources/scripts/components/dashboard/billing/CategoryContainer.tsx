import React, { useEffect, useState } from 'react';
import { RouteComponentProps } from "react-router-dom";
import PageContentBlock from '@/components/elements/PageContentBlock';
import ContentBox from '@/components/elements/ContentBox';
import tw from 'twin.macro';
import FlashMessageRender from '@/components/FlashMessageRender';
import Spinner from '@/components/elements/Spinner';
import useFlash from '@/plugins/useFlash';
import useSWR from 'swr';
import { Link } from 'react-router-dom';
import { number, object, string } from 'yup';
import { Field as FormikField, Form, Formik, FormikHelpers } from 'formik';
import FormikFieldWrapper from '@/components/elements/FormikFieldWrapper';
import Field from '@/components/elements/Field';
import Button from '@/components/elements/Button';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import GreyRowBox from '@/components/elements/GreyRowBox';
import MessageBox from '@/components/MessageBox';

import getCategory from '@/api/billing/getCategory';
import addProduct from '@/api/billing/addProduct';

export interface CategoryResponse {
    settings: any[];
    products: any[];
    cart: any[];
}

interface CreateValues {
    product_id: string;
}

type Props = {
    id: string;
}


export default ({ match }: RouteComponentProps<Props>) => {

    var id = match.params.id;

    const { addFlash, clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error, mutate } = useSWR<CategoryResponse>([ id, '/product' ], (id) => getCategory(id));

    const [ isSubmit, setSubmit ] = useState(false);

    useEffect(() => {
        if (!error) {
            clearFlashes('product');
        } else {
            clearAndAddHttpError({ key: 'product', error });
        }
    });

    const submit = ({ product_id }: CreateValues, { setSubmitting }: FormikHelpers<CreateValues>) => {
        clearFlashes('product');
        clearFlashes('product:add');
        setSubmitting(false);
        setSubmit(true);

        console.log(product_id);

        addProduct(product_id).then(() => {
            mutate();
            setSubmit(false);
        })
        .then(() => addFlash({
            type: 'success',
            key: 'product:add',
            message: 'Your product has been added to cart.',
        }))
        .catch(error => {
            setSubmitting(false);
            setSubmit(false);
            clearAndAddHttpError({ key: 'product:add', error });
        });

    };

    return (
        <PageContentBlock title={'Store'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full lg:pl-4`}>
                <FlashMessageRender byKey={'category'} css={tw`mb-4`} />
            </div>
            <div css={tw`w-full lg:pl-4`}>
                <FlashMessageRender byKey={'category:add'} css={tw`mb-4`} />
            </div>
            {!data ?
                <div css={tw`w-full lg:pl-4`}>
                    <Spinner size={'large'} centered />
                </div>
                :
                <>
                    <div css={tw`w-full lg:pl-4`}>
                        {data.cart.length > 0 ?
                            <GreyRowBox $hoverable={false} css={tw`mb-2`}>
                                <div css={tw`flex-1 ml-4`}>
                                    You have products in your cart !
                                </div>
                                <div css={tw`flex ml-48 justify-end`}>
                                    <Link to={`/billing/store/checkout`}>
                                        <Button type={'submit'} size={'xsmall'} css={'float: right;'} color={'primary'}>Checkout</Button>
                                    </Link>
                                </div>
                            </GreyRowBox>
                        : null
                        }   
                    </div>
                    {data.products.length < 1 ?
                        <div css={tw`w-full lg:pl-4`}>
                            <MessageBox type="info" title="Info">
                                There are no products.
                            </MessageBox>
                        </div>
                        :
                        (data.products.map((item, key) => (
                            <div css={tw`w-full lg:w-3/12 lg:pl-4`} key={key}>
                                <TitledGreyBox title={item.name}>
                                    <div css={tw`px-1 py-2 justify-center text-center`}>
                                        {data.settings[0]?.products_img === 1 ?
                                            <>
                                                {data.settings[0]?.products_img_rounded === 1 ?
                                                <img 
                                                    src={item.img} 
                                                    css={tw`rounded-full flex items-center justify-center m-auto`}
                                                    width={data.settings[0]?.products_img_width} 
                                                    height={data.settings[0]?.products_img_height} />
                                                : 
                                                <img 
                                                    src={item.img} 
                                                    css={tw`flex items-center justify-center m-auto`}
                                                    width={data.settings[0]?.products_img_width} 
                                                    height={data.settings[0]?.products_img_height} />
                                                }
                                            </>
                                        : null
                                        }
                                        <br></br>
                                        <span dangerouslySetInnerHTML={{ __html: item.description }}></span>
                                        <br></br>
                                        <div css={tw`w-full pt-4`}>
                                            <span css={'float: left;'}>{item.price} <span dangerouslySetInnerHTML={{ __html: item.code }}></span></span>
                                            <Formik
                                                onSubmit={submit}
                                                initialValues={{ product_id: item.id }}
                                                validationSchema={object().shape({})}
                                            >
                                                <Form>
                                                    <div css={tw`flex flex-wrap`}>
                                                        <div css={tw`w-full lg:w-6/12`}>
                                                            <Field 
                                                                name={'product_id'}
                                                                type={'hidden'}
                                                            />
                                                        </div>
                                                    </div>
                                                    <div css={tw`flex justify-end`}>
                                                        <Button type={'submit'} size={'xsmall'} css={'float: right;'} disabled={isSubmit} color={'primary'}>Order</Button>
                                                    </div>
                                                </Form>
                                            </Formik> 
                                        </div>
                                    </div>
                                </TitledGreyBox>
                                <br></br>
                            </div>
                        )))
                    }
                </>
            }
        </PageContentBlock>
    );
};
