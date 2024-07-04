import React, { useEffect, useState } from 'react';
import PageContentBlock from '@/components/elements/PageContentBlock';
import ContentBox from '@/components/elements/ContentBox';
import tw from 'twin.macro';
import FlashMessageRender from '@/components/FlashMessageRender';
import Spinner from '@/components/elements/Spinner';
import useFlash from '@/plugins/useFlash';
import useSWR from 'swr';
import { Link } from 'react-router-dom';
import Button from '@/components/elements/Button';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import GreyRowBox from '@/components/elements/GreyRowBox';
import MessageBox from '@/components/MessageBox';

import getCategories from '@/api/billing/getCategories';

export interface CategoriesResponse {
    settings: any[];
    categories: any[];
    cart: any[];
}

export default () => {
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error } = useSWR<CategoriesResponse>([ '/categories' ], () => getCategories());

    useEffect(() => {
        if (!error) {
            clearFlashes('categories');
        } else {
            clearAndAddHttpError({ key: 'categories', error });
        }
    });

    return (
        <>
            <div css={tw`w-full lg:pl-4`}>
                <FlashMessageRender byKey={'categories'} css={tw`mb-4`} />
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
                    {data.categories.length < 1 ?
                        <div css={tw`w-full lg:pl-4`}>
                            <MessageBox type="info" title="Info">
                                There are no categories.
                            </MessageBox>
                        </div>
                        :
                        (data.categories.map((item, key) => (
                            <div css={tw`w-full lg:w-3/12 lg:pl-4`} key={key}>
                                <TitledGreyBox title={item.name}>
                                    <div css={tw`px-1 py-2 justify-center text-center`}>
                                        {data.settings[0]?.categories_img === 1 ?
                                            <>
                                                {data.settings[0]?.categories_img_rounded === 1 ?
                                                <img 
                                                    src={item.img} 
                                                    css={tw`rounded-full flex items-center justify-center m-auto`}
                                                    width={data.settings[0]?.categories_img_width} 
                                                    height={data.settings[0]?.categories_img_height} />
                                                : 
                                                <img 
                                                    src={item.img} 
                                                    css={tw`flex items-center justify-center m-auto`}
                                                    width={data.settings[0]?.categories_img_width} 
                                                    height={data.settings[0]?.categories_img_height} />
                                                }
                                            </>
                                        : null
                                        }
                                        <br></br>
                                        <span dangerouslySetInnerHTML={{ __html: item.description }}></span>
                                        <br></br>
                                        <div css={tw`w-full pt-4`}>
                                            <div css={tw`flex justify-end`}>
                                                <Link to={`/billing/store/category/${item.id}`}>
                                                    <Button size={'xsmall'} css={'float: right;'} color={'primary'}>View Products</Button>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </TitledGreyBox>
                                <br></br>
                            </div>
                        )))
                    }
                </>
            }
        </>
    );
};
