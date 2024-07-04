import React, { useEffect, useState } from 'react';
import PageContentBlock from '@/components/elements/PageContentBlock';
import useFlash from '@/plugins/useFlash';
import tw from 'twin.macro';
import useSWR from 'swr';
import Spinner from '@/components/elements/Spinner';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import { Field as FormikField, Form, Formik, FormikHelpers } from 'formik';
import FormikFieldWrapper from '@/components/elements/FormikFieldWrapper';
import Button from '@/components/elements/Button';
import { number, object, string } from 'yup';
import Field from '@/components/elements/Field';
import Label from '@/components/elements/Label';
import Select from '@/components/elements/Select';
import GreyRowBox from '@/components/elements/GreyRowBox';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faServer } from '@fortawesome/free-solid-svg-icons';
import styled from 'styled-components/macro';
import { Link } from 'react-router-dom';
import FlashMessageRender from '@/components/FlashMessageRender';

import getCheckout from '@/api/billing/getCheckout';
import DeleteButton from '@/components/dashboard/billing/DeleteButton';
import EmptyButton from '@/components/dashboard/billing/EmptyButton';
import CheckoutButton from '@/components/dashboard/billing/CheckoutButton';

const Code = styled.code`${tw`font-mono py-1 px-2 bg-neutral-900 rounded text-sm inline-block`}`;

export interface CheckoutResponse {
    cart: any[];
    billing: any[];
    total_price: number;
    balance: number;
}

/*interface CreateValues {
    server: number;
    message: string;
}*/

export default () => {
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error, mutate } = useSWR<CheckoutResponse>([ '/checkout' ], () => getCheckout());

    const [ isSubmit, setSubmit ] = useState(false);

    useEffect(() => {
        if (!error) {
            clearFlashes('checkout');
        } else {
            clearAndAddHttpError({ key: 'checkout', error });
        }
    });


    return (
        <PageContentBlock title={'Checkout'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full lg:pl-4`}>
                <FlashMessageRender byKey={'checkout'} css={tw`mb-4`} />
            </div>
            <div css={tw`w-full lg:pl-4`}>
                <FlashMessageRender byKey={'checkout:create'} css={tw`mb-4`} />
            </div>
             {!data ?
                (
                    <div css={tw`w-full lg:pl-4`}>
                        <Spinner size={'large'} centered />
                    </div>
                )
                :
                (
                    <>
                        <div css={tw`w-full lg:pl-4`}>
                            <GreyRowBox $hoverable={false} css={tw`mb-2`}>
                                <div css={tw`flex-1 ml-4`}>
                                    You want to add more products to cart ?
                                </div>
                                <div css={tw`flex ml-48 justify-end`}>
                                    <Link to={`/billing/store/`}>
                                        <Button type={'submit'} size={'xsmall'} color={'primary'}>Continue Shoping</Button>
                                    </Link>
                                </div>
                            </GreyRowBox>
                        </div>
                        <div css={tw`w-full lg:w-8/12 mt-4 lg:pl-4`}>
                            {data.cart.length < 1 ?
                                <p css={tw`text-center text-sm text-neutral-400 pt-4 pb-4`}>
                                    There are no products in your cart.
                                </p>
                                :
                                (data.cart.map((item, key) => (
                                    <GreyRowBox $hoverable={false} css={tw`mb-2`} key={key}>
                                            <div css={tw`flex items-center w-full md:w-auto`}>
                                                <div css={tw`hidden md:block`}>
                                                    <FontAwesomeIcon icon={faServer} fixedWidth/>
                                                </div>
                                                <div css={tw`flex-1 ml-4`}>
                                                    <p css={tw`text-sm`}>{item.product.name}</p>
                                                    <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Product</p>
                                                </div>
                                                <div css={tw`ml-48 text-center hidden md:block`}>
                                                    <p css={tw`text-sm`}>{item.product.memory}</p>
                                                    <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Memory</p>
                                                </div>
                                                <div css={tw`ml-8 text-center hidden md:block`}>
                                                    <p css={tw`text-sm`}>{item.product.disk}</p>
                                                    <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Disk</p>
                                                </div>
                                                <div css={tw`ml-8 text-center`}>
                                                    <p css={tw`text-sm`}>{item.quantity}</p>
                                                    <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Quantity</p>
                                                </div>
                                                <div css={tw`ml-8 text-center`}>
                                                    <p css={tw`text-sm`}>{item.price} <span dangerouslySetInnerHTML={{ __html: item.code }}></span></p>
                                                    <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Price</p>
                                                </div>
                                                <div css={tw`ml-8 text-center`}>
                                                    <DeleteButton id={item.product_id} onDeleted={() => mutate()}></DeleteButton>
                                                    <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Action</p>
                                                </div>
                                            </div>
                                    </GreyRowBox>
                                )))
                            }
                        </div>
                        <br></br>
                        <div css={tw`w-full lg:w-4/12 lg:pl-4`}>
                            <TitledGreyBox title={'Summary'}>
                                <GreyRowBox $hoverable={false} css={tw`mb-2`}>
                                    <div css={tw`flex w-full md:w-auto`}>
                                        <div css={tw`text-sm flex-1 text-left`}>
                                            Total Price:
                                        </div>
                                        <div css={tw`text-sm ml-16 text-right justify-end`}>
                                            {data.total_price} <span dangerouslySetInnerHTML={{ __html: data.billing[0]?.code }}></span>
                                        </div>
                                    </div>
                                </GreyRowBox>
                                <GreyRowBox $hoverable={false} css={tw`mb-2`}>
                                    <div css={tw`flex w-full md:w-auto`}>
                                        <div css={tw`text-sm flex-1 text-left`}>
                                            Your Balance: 
                                        </div>
                                        <div css={tw`text-sm ml-16 text-right justify-end`}>
                                            {data.balance} <span dangerouslySetInnerHTML={{ __html: data.billing[0]?.code }}></span>
                                        </div>
                                    </div>
                                </GreyRowBox>
                                <GreyRowBox $hoverable={false} css={tw`mb-2`}>
                                    <div css={tw`flex w-full md:w-auto`}>
                                        <div css={tw`text-sm flex-1 text-left`}>
                                            Fees:
                                        </div>
                                        <div css={tw`text-sm ml-16 text-right justify-end`}>
                                            0 <span dangerouslySetInnerHTML={{ __html: data.billing[0]?.code }}></span>
                                        </div>
                                    </div>
                                </GreyRowBox>
                                <GreyRowBox $hoverable={false} css={tw`mb-2`}>
                                    <div css={tw`flex w-full md:w-auto`}>
                                        <div css={tw`text-sm flex-1 text-left`}>
                                            <EmptyButton id={1} onDeleted={() => mutate()}></EmptyButton>
                                        </div>
                                        <div css={tw`text-sm ml-16 text-right justify-end`}>
                                            <CheckoutButton id={1} onDeleted={() => mutate()}></CheckoutButton>
                                        </div>
                                    </div>
                                </GreyRowBox>
                            </TitledGreyBox>
                        </div>
                    </>
                )
            }
        </PageContentBlock>
    );
};
